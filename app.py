from flask import Flask, render_template, request, redirect, url_for, session
import sqlite3
import qrcode
import os
from functools import wraps
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.config["SECRET_KEY"] = os.environ.get("SECRET_KEY", "change-me-in-production")

conn = sqlite3.connect("database.db")
cur = conn.cursor()

cur.execute("""
CREATE TABLE IF NOT EXISTS users (
id INTEGER PRIMARY KEY AUTOINCREMENT,
email TEXT,
username TEXT UNIQUE,
password_hash TEXT,
is_admin INTEGER NOT NULL DEFAULT 1,
provider TEXT DEFAULT 'local'
)
""")

cur.execute("""
CREATE TABLE IF NOT EXISTS employees (
id INTEGER PRIMARY KEY AUTOINCREMENT,
name TEXT NOT NULL,
scans INTEGER NOT NULL DEFAULT 0,
good_count INTEGER NOT NULL DEFAULT 0,
ok_count INTEGER NOT NULL DEFAULT 0,
bad_count INTEGER NOT NULL DEFAULT 0
)
""")

cur.execute("""
CREATE TABLE IF NOT EXISTS feedback (
id INTEGER PRIMARY KEY AUTOINCREMENT,
employee_id INTEGER,
rating TEXT,
comment TEXT
)
""")

# Lightweight migration for older deployments: add per-rating columns if they are missing
try:
    cur.execute("ALTER TABLE employees ADD COLUMN good_count INTEGER NOT NULL DEFAULT 0")
except sqlite3.OperationalError:
    pass

try:
    cur.execute("ALTER TABLE employees ADD COLUMN ok_count INTEGER NOT NULL DEFAULT 0")
except sqlite3.OperationalError:
    pass

try:
    cur.execute("ALTER TABLE employees ADD COLUMN bad_count INTEGER NOT NULL DEFAULT 0")
except sqlite3.OperationalError:
    pass

conn.commit()
cur.close()
conn.close()

def login_required(view_func):
    @wraps(view_func)
    def wrapped_view(*args, **kwargs):
        if not session.get("admin_logged_in"):
            return redirect(url_for("login"))
        return view_func(*args, **kwargs)

    return wrapped_view


@app.route("/")
def home():
    if session.get("admin_logged_in"):
        return redirect(url_for("admin"))
    return redirect(url_for("login"))


@app.route("/login", methods=["GET", "POST"])
def login():
    error = None
    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "").strip()

        conn = sqlite3.connect("database.db")
        cur = conn.cursor()
        cur.execute("SELECT id, username, password_hash, is_admin FROM users WHERE username = ?", (username,))
        row = cur.fetchone()
        conn.close()

        if row and row[2] and check_password_hash(row[2], password):
            session["admin_logged_in"] = bool(row[3])
            session["user_id"] = row[0]
            session["username"] = row[1]
            return redirect(url_for("admin"))

        error = "Invalid credentials. Please try again."

    return render_template("login.html", error=error)


@app.route("/signup", methods=["GET", "POST"])
def signup():
    error = None
    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "").strip()

        if not username or not password:
            error = "Username and password are required."
        else:
            conn = sqlite3.connect("database.db")
            cur = conn.cursor()
            cur.execute("SELECT id FROM users WHERE username = ?", (username,))
            existing = cur.fetchone()

            if existing:
                error = "That username is already taken."
            else:
                password_hash = generate_password_hash(password)
                cur.execute(
                    "INSERT INTO users (username, password_hash, is_admin, provider) VALUES (?, ?, ?, ?)",
                    (username, password_hash, 1, "local"),
                )
                conn.commit()
                conn.close()
                return redirect(url_for("login"))

            conn.close()

    return render_template("signup.html", error=error)


@app.route("/logout")
def logout():
    session.clear()
    return redirect(url_for("login"))


@app.route("/admin")
@login_required
def admin():

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    # Ensure the employees table exists (safety for fresh deployments)
    cur.execute("""
    CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    scans INTEGER NOT NULL DEFAULT 0
    )
    """)

    cur.execute("SELECT * FROM employees")
    employees = cur.fetchall()

    conn.close()

    return render_template("admin.html", employees=employees)

@app.route("/add_employee", methods=["POST"])
@login_required
def add_employee():

    name = request.form["name"]

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute("INSERT INTO employees (name) VALUES (?)",(name,))
    employee_id = cur.lastrowid

    conn.commit()
    conn.close()

    # generate QR code pointing to the hosted review URL
    url = url_for("review", employee_id=employee_id, _external=True)

    os.makedirs("static/qrcodes", exist_ok=True)
    img = qrcode.make(url)
    img.save(f"static/qrcodes/{employee_id}.png")

    return redirect(url_for("admin"))


@app.route("/edit_employee/<int:employee_id>", methods=["POST"])
@login_required
def edit_employee(employee_id):
    new_name = request.form.get("name", "").strip()
    if new_name:
        conn = sqlite3.connect("database.db")
        cur = conn.cursor()
        cur.execute("UPDATE employees SET name = ? WHERE id = ?", (new_name, employee_id))
        conn.commit()
        conn.close()
    return redirect(url_for("admin"))


@app.route("/delete_employee/<int:employee_id>", methods=["POST"])
@login_required
def delete_employee(employee_id):
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    # Delete associated feedback first to keep integrity simple
    cur.execute("DELETE FROM feedback WHERE employee_id = ?", (employee_id,))
    cur.execute("DELETE FROM employees WHERE id = ?", (employee_id,))

    conn.commit()
    conn.close()
    return redirect(url_for("admin"))

@app.route("/review/<employee_id>")
def review(employee_id):
    return render_template("feedback.html", employee_id=employee_id)
# SERVER STARTS HERE
@app.route("/good/<employee_id>")
def good(employee_id):

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute(
        "UPDATE employees SET scans = scans + 1, good_count = good_count + 1 WHERE id=?",
        (employee_id,)
    )

    # Record a "good" rating so it shows up in the per-employee matrix
    cur.execute(
        "INSERT INTO feedback (employee_id, rating, comment) VALUES (?, ?, ?)",
        (employee_id, "good", ""),
    )


    conn.commit()
    conn.close()

    return redirect("https://g.page/r/CQVXCUw7251qEBM/review")

@app.route("/ok/<employee_id>")
def ok(employee_id):
    return render_template("internal_feedback.html",
                           employee_id=employee_id,
                           rating="ok")

@app.route("/bad/<employee_id>")
def bad(employee_id):
    return render_template("internal_feedback.html",
                           employee_id=employee_id,
                           rating="bad")

@app.route("/submit_internal_feedback", methods=["POST"])
def submit_internal_feedback():

    print("FORM SUBMITTED")   # add this

    employee_id = request.form["employee_id"]
    rating = request.form["rating"]
    comment = request.form["comment"]

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute(
        "INSERT INTO feedback (employee_id, rating, comment) VALUES (?, ?, ?)", (employee_id, rating, comment)
    )

    # Update total scans and per-rating counters
    if rating == "ok":
        cur.execute(
            "UPDATE employees SET scans = scans + 1, ok_count = ok_count + 1 WHERE id=?",
            (employee_id,),
        )
    elif rating == "bad":
        cur.execute(
            "UPDATE employees SET scans = scans + 1, bad_count = bad_count + 1 WHERE id=?",
            (employee_id,),
        )

    conn.commit()
    conn.close()

    return redirect("/thankyou")

@app.route("/feedback")
@login_required
def feedback():

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    # Ensure the employees table exists before joining
    cur.execute("""
    CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    scans INTEGER NOT NULL DEFAULT 0
    )
    """)

    cur.execute("""
    SELECT employees.name, feedback.rating, feedback.comment
    FROM feedback
    JOIN employees ON feedback.employee_id = employees.id""")

    feedback_list = cur.fetchall()

    conn.close()

    return render_template("feedback_list.html", feedback=feedback_list)

@app.route("/thankyou")
def thankyou():
    return render_template("thankyou.html")

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
