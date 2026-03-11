from flask import Flask, render_template, request, redirect, url_for, session, Response
import sqlite3
import qrcode
import os
from functools import wraps
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.config["SECRET_KEY"] = os.environ.get("SECRET_KEY", "change-me-in-production")

BRAND_NAME = os.environ.get("BRAND_NAME", "ReviewTracker")
BRAND_TAGLINE = os.environ.get("BRAND_TAGLINE", "Employee QR review funnel")
BRAND_LOGO_URL = os.environ.get("BRAND_LOGO_URL")

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
bad_count INTEGER NOT NULL DEFAULT 0,
employee_username TEXT UNIQUE,
employee_password_hash TEXT
)
""")

cur.execute("""
CREATE TABLE IF NOT EXISTS feedback (
id INTEGER PRIMARY KEY AUTOINCREMENT,
employee_id INTEGER,
rating TEXT,
comment TEXT,
created_at TEXT DEFAULT (CURRENT_TIMESTAMP),
status TEXT NOT NULL DEFAULT 'new'
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

try:
    cur.execute("ALTER TABLE employees ADD COLUMN employee_username TEXT UNIQUE")
except sqlite3.OperationalError:
    pass

try:
    cur.execute("ALTER TABLE employees ADD COLUMN employee_password_hash TEXT")
except sqlite3.OperationalError:
    pass

# Ensure feedback has created_at and status for old databases
try:
    cur.execute("ALTER TABLE feedback ADD COLUMN created_at TEXT DEFAULT (CURRENT_TIMESTAMP)")
except sqlite3.OperationalError:
    pass

try:
    cur.execute("ALTER TABLE feedback ADD COLUMN status TEXT NOT NULL DEFAULT 'new'")
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


def employee_login_required(view_func):
    @wraps(view_func)
    def wrapped_view(*args, **kwargs):
        if not session.get("employee_id"):
            return redirect(url_for("employee_login"))
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

    return render_template("login.html", error=error, brand_name=BRAND_NAME, brand_tagline=BRAND_TAGLINE, brand_logo_url=BRAND_LOGO_URL)


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

    return render_template("signup.html", error=error, brand_name=BRAND_NAME, brand_tagline=BRAND_TAGLINE, brand_logo_url=BRAND_LOGO_URL)


@app.route("/employee/login", methods=["GET", "POST"])
def employee_login():
    error = None
    if request.method == "POST":
        username = request.form.get("username", "").strip()
        password = request.form.get("password", "").strip()

        conn = sqlite3.connect("database.db")
        cur = conn.cursor()
        cur.execute(
            "SELECT id, name, employee_password_hash FROM employees WHERE employee_username = ?",
            (username,),
        )
        row = cur.fetchone()
        conn.close()

        if row and row[2] and check_password_hash(row[2], password):
            session.clear()
            session["employee_id"] = row[0]
            session["employee_name"] = row[1]
            return redirect(url_for("employee_dashboard"))

        error = "Invalid employee credentials. Please try again."

    return render_template("employee_login.html", error=error, brand_name=BRAND_NAME, brand_tagline=BRAND_TAGLINE, brand_logo_url=BRAND_LOGO_URL)


@app.route("/employee/logout")
def employee_logout():
    session.clear()
    return redirect(url_for("employee_login"))


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

    cur.execute("SELECT id, name, scans, good_count, ok_count, bad_count FROM employees")
    employees = cur.fetchall()

    conn.close()

    return render_template("admin.html", employees=employees, brand_name=BRAND_NAME, brand_tagline=BRAND_TAGLINE, brand_logo_url=BRAND_LOGO_URL)

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


@app.route("/employee/<int:employee_id>/credentials", methods=["POST"])
@login_required
def update_employee_credentials(employee_id):
    username = request.form.get("employee_username", "").strip()
    password = request.form.get("employee_password", "").strip()

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    if username and password:
        pw_hash = generate_password_hash(password)
        cur.execute(
            "UPDATE employees SET employee_username = ?, employee_password_hash = ? WHERE id = ?",
            (username, pw_hash, employee_id),
        )
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

    cur.execute("""
    SELECT feedback.id,
           employees.name,
           feedback.rating,
           feedback.comment,
           feedback.status,
           feedback.created_at
    FROM feedback
    JOIN employees ON feedback.employee_id = employees.id
    ORDER BY datetime(feedback.created_at) DESC, feedback.id DESC
    """)

    feedback_list = cur.fetchall()

    conn.close()

    return render_template("feedback_list.html", feedback=feedback_list, brand_name=BRAND_NAME, brand_tagline=BRAND_TAGLINE, brand_logo_url=BRAND_LOGO_URL)


@app.route("/feedback/<int:feedback_id>/status", methods=["POST"])
@login_required
def update_feedback_status(feedback_id: int):
    new_status = request.form.get("status", "resolved")
    if new_status not in ("new", "in_progress", "resolved"):
        new_status = "resolved"

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute("UPDATE feedback SET status = ? WHERE id = ?", (new_status, feedback_id))
    conn.commit()
    conn.close()

    return redirect(url_for("feedback"))


@app.route("/export/employees.csv")
@login_required
def export_employees_csv():
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute("""
    SELECT id, name, scans, good_count, ok_count, bad_count
    FROM employees
    ORDER BY scans DESC, name ASC
    """)
    rows = cur.fetchall()
    conn.close()

    lines = ["id,name,scans,good_count,ok_count,bad_count"]
    for r in rows:
        # basic CSV escaping for commas/quotes in name
        name = str(r[1]).replace('"', '""')
        lines.append(f'{r[0]},"{name}",{r[2]},{r[3]},{r[4]},{r[5]}')

    csv_data = "\n".join(lines)
    return Response(
        csv_data,
        mimetype="text/csv",
        headers={"Content-Disposition": "attachment; filename=employees.csv"},
    )


@app.route("/export/feedback.csv")
@login_required
def export_feedback_csv():
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute("""
    SELECT feedback.id,
           feedback.employee_id,
           employees.name,
           feedback.rating,
           feedback.comment,
           feedback.status,
           feedback.created_at
    FROM feedback
    JOIN employees ON feedback.employee_id = employees.id
    ORDER BY datetime(feedback.created_at) DESC, feedback.id DESC
    """)
    rows = cur.fetchall()
    conn.close()

    lines = ["id,employee_id,employee_name,rating,comment,status,created_at"]
    for r in rows:
        employee_name = str(r[2]).replace('"', '""')
        comment = (r[4] or "").replace('"', '""')
        lines.append(
            f'{r[0]},{r[1]},"{employee_name}",{r[3]},"{comment}",{r[5]},{r[6]}'
        )

    csv_data = "\n".join(lines)
    return Response(
        csv_data,
        mimetype="text/csv",
        headers={"Content-Disposition": "attachment; filename=feedback.csv"},
    )


@app.route("/employee/dashboard")
@employee_login_required
def employee_dashboard():
    employee_id = session.get("employee_id")
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute(
        "SELECT id, name, scans, good_count, ok_count, bad_count FROM employees WHERE id = ?",
        (employee_id,),
    )
    employee = cur.fetchone()

    cur.execute(
        "SELECT id, name, scans, good_count, ok_count, bad_count "
        "FROM employees ORDER BY scans DESC, name ASC"
    )
    leaderboard = cur.fetchall()

    cur.execute(
        "SELECT rating, comment, status, created_at "
        "FROM feedback WHERE employee_id = ? "
        "ORDER BY datetime(created_at) DESC, id DESC",
        (employee_id,),
    )
    feedback_rows = cur.fetchall()

    conn.close()

    return render_template(
        "employee_dashboard.html",
        employee=employee,
        leaderboard=leaderboard,
        feedback_rows=feedback_rows,
        brand_name=BRAND_NAME,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=BRAND_LOGO_URL,
    )


@app.route("/analytics")
@login_required
def analytics():
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    # Daily breakdown
    cur.execute("""
    SELECT DATE(created_at) as day,
           COUNT(*) as total,
           SUM(CASE WHEN rating = 'good' THEN 1 ELSE 0 END) as good_count,
           SUM(CASE WHEN rating = 'ok' THEN 1 ELSE 0 END) as ok_count,
           SUM(CASE WHEN rating = 'bad' THEN 1 ELSE 0 END) as bad_count
    FROM feedback
    GROUP BY day
    ORDER BY day DESC
    """)
    daily_stats = cur.fetchall()

    # Last 30 days per employee
    cur.execute("""
    SELECT e.id,
           e.name,
           COALESCE(SUM(CASE WHEN f.rating = 'good' THEN 1 ELSE 0 END), 0) as good_30d,
           COALESCE(SUM(CASE WHEN f.rating = 'ok' THEN 1 ELSE 0 END), 0) as ok_30d,
           COALESCE(SUM(CASE WHEN f.rating = 'bad' THEN 1 ELSE 0 END), 0) as bad_30d
    FROM employees e
    LEFT JOIN feedback f
      ON f.employee_id = e.id
     AND DATE(f.created_at) >= DATE('now', '-30 day')
    GROUP BY e.id, e.name
    ORDER BY (good_30d + ok_30d + bad_30d) DESC, e.name ASC
    """)
    per_employee_30d = cur.fetchall()

    conn.close()

    return render_template(
        "analytics.html",
        daily_stats=daily_stats,
        per_employee_30d=per_employee_30d,
        brand_name=BRAND_NAME,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=BRAND_LOGO_URL,
    )

@app.route("/thankyou")
def thankyou():
    return render_template("thankyou.html")

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
