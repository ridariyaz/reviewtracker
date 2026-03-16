from flask import Flask, render_template, request, redirect, url_for, session, Response
import sqlite3
import qrcode
import os
from functools import wraps
from werkzeug.security import generate_password_hash, check_password_hash
from datetime import datetime, timedelta

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
CREATE TABLE IF NOT EXISTS companies (
id INTEGER PRIMARY KEY AUTOINCREMENT,
user_id INTEGER NOT NULL,
name TEXT NOT NULL,
logo_url TEXT,
primary_color TEXT DEFAULT '#0d6efd',
secondary_color TEXT DEFAULT '#111827',
google_review_url TEXT,
created_at TEXT DEFAULT (CURRENT_TIMESTAMP)
)
""")

cur.execute("""
CREATE TABLE IF NOT EXISTS employees (
id INTEGER PRIMARY KEY AUTOINCREMENT,
company_id INTEGER NOT NULL DEFAULT 1,
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
company_id INTEGER NOT NULL DEFAULT 1,
employee_id INTEGER,
rating TEXT,
comment TEXT,
created_at TEXT DEFAULT (CURRENT_TIMESTAMP),
status TEXT NOT NULL DEFAULT 'new'
)
""")

# Lightweight migration for older deployments: add per-rating and company columns if they are missing
try:
    cur.execute("ALTER TABLE employees ADD COLUMN company_id INTEGER NOT NULL DEFAULT 1")
except sqlite3.OperationalError:
    pass

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

try:
    cur.execute("ALTER TABLE feedback ADD COLUMN company_id INTEGER NOT NULL DEFAULT 1")
except sqlite3.OperationalError:
    pass

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


def get_companies_for_user(user_id: int | None):
    if not user_id:
        return []
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute(
        "SELECT id, name, logo_url, primary_color, secondary_color, google_review_url "
        "FROM companies WHERE user_id = ? ORDER BY id",
        (user_id,),
    )
    rows = cur.fetchall()
    conn.close()
    return [
        {
            "id": r[0],
            "name": r[1],
            "logo_url": r[2],
            "primary_color": r[3],
            "secondary_color": r[4],
            "google_review_url": r[5],
        }
        for r in rows
    ]


def get_current_company(user_id: int | None):
    if not user_id:
        return None

    company_id = session.get("company_id")
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    if company_id is not None:
        cur.execute(
            "SELECT id, name, logo_url, primary_color, secondary_color, google_review_url "
            "FROM companies WHERE id = ? AND user_id = ?",
            (company_id, user_id),
        )
        row = cur.fetchone()
        if row:
            conn.close()
            return {
                "id": row[0],
                "name": row[1],
                "logo_url": row[2],
                "primary_color": row[3],
                "secondary_color": row[4],
                "google_review_url": row[5],
            }

    cur.execute(
        "SELECT id, name, logo_url, primary_color, secondary_color, google_review_url "
        "FROM companies WHERE user_id = ? ORDER BY id LIMIT 1",
        (user_id,),
    )
    row = cur.fetchone()
    conn.close()

    if row:
        session["company_id"] = row[0]
        return {
            "id": row[0],
            "name": row[1],
            "logo_url": row[2],
            "primary_color": row[3],
            "secondary_color": row[4],
            "google_review_url": row[5],
        }

    return None


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

        if row and row[2] and check_password_hash(row[2], password):
            user_id = row[0]
            session["admin_logged_in"] = bool(row[3])
            session["user_id"] = user_id
            session["username"] = row[1]

            # Ensure at least one company exists for this user
            cur.execute("SELECT id FROM companies WHERE user_id = ? ORDER BY id LIMIT 1", (user_id,))
            company = cur.fetchone()
            if not company:
                cur.execute(
                    "INSERT INTO companies (user_id, name) VALUES (?, ?)",
                    (user_id, f"{row[1]}'s Company"),
                )
                conn.commit()
                company_id = cur.lastrowid
            else:
                company_id = company[0]

            session["company_id"] = company_id
            conn.close()
            return redirect(url_for("admin"))

        conn.close()

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


@app.route("/companies")
@login_required
def companies_settings():
    user_id = session.get("user_id")
    companies = get_companies_for_user(user_id)
    current_company = get_current_company(user_id)
    return render_template(
        "companies.html",
        companies=companies,
        current_company=current_company,
        brand_name=current_company["name"] if current_company else BRAND_NAME,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=current_company["logo_url"] if current_company else BRAND_LOGO_URL,
    )


@app.route("/companies/create", methods=["POST"])
@login_required
def create_company():
    user_id = session.get("user_id")
    name = request.form.get("name", "").strip()
    if not name:
        return redirect(url_for("companies_settings"))

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute(
        "INSERT INTO companies (user_id, name, logo_url, primary_color, secondary_color, google_review_url) "
        "VALUES (?, ?, ?, ?, ?, ?)",
        (
            user_id,
            name,
            request.form.get("logo_url", "").strip() or None,
            request.form.get("primary_color", "").strip() or "#0d6efd",
            request.form.get("secondary_color", "").strip() or "#111827",
            request.form.get("google_review_url", "").strip() or None,
        ),
    )
    conn.commit()
    company_id = cur.lastrowid
    conn.close()

    session["company_id"] = company_id
    return redirect(url_for("companies_settings"))


@app.route("/companies/<int:company_id>/update", methods=["POST"])
@login_required
def update_company(company_id: int):
    user_id = session.get("user_id")

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute("SELECT id FROM companies WHERE id = ? AND user_id = ?", (company_id, user_id))
    row = cur.fetchone()
    if not row:
        conn.close()
        return redirect(url_for("companies_settings"))

    cur.execute(
        "UPDATE companies SET name = ?, logo_url = ?, primary_color = ?, secondary_color = ?, google_review_url = ? "
        "WHERE id = ? AND user_id = ?",
        (
            request.form.get("name", "").strip() or "Company",
            request.form.get("logo_url", "").strip() or None,
            request.form.get("primary_color", "").strip() or "#0d6efd",
            request.form.get("secondary_color", "").strip() or "#111827",
            request.form.get("google_review_url", "").strip() or None,
            company_id,
            user_id,
        ),
    )
    conn.commit()
    conn.close()
    return redirect(url_for("companies_settings"))


@app.route("/companies/switch", methods=["POST"])
@login_required
def switch_company():
    user_id = session.get("user_id")
    company_id = request.form.get("company_id")
    try:
        company_id_int = int(company_id)
    except (TypeError, ValueError):
        return redirect(url_for("admin"))

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute("SELECT id FROM companies WHERE id = ? AND user_id = ?", (company_id_int, user_id))
    row = cur.fetchone()
    conn.close()

    if row:
        session["company_id"] = company_id_int

    return redirect(request.referrer or url_for("admin"))


@app.route("/admin")
@login_required
def admin():

    user_id = session.get("user_id")
    company = get_current_company(user_id)
    companies = get_companies_for_user(user_id)

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute(
        "SELECT id, name, scans, good_count, ok_count, bad_count "
        "FROM employees WHERE company_id = ?",
        (company["id"],),
    )
    employees = cur.fetchall()

    conn.close()

    return render_template(
        "admin.html",
        employees=employees,
        brand_name=company["name"] if company else BRAND_NAME,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=company["logo_url"] if company else BRAND_LOGO_URL,
        companies=companies,
        current_company=company,
    )

@app.route("/add_employee", methods=["POST"])
@login_required
def add_employee():

    name = request.form["name"]

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    company = get_current_company(session.get("user_id"))
    company_id = company["id"] if company else 1

    cur.execute("INSERT INTO employees (company_id, name) VALUES (?, ?)", (company_id, name))
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
    # For now we only brand by name/logo; company colors are handled in CSS variables
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute(
        "SELECT c.name, c.logo_url FROM employees e "
        "JOIN companies c ON e.company_id = c.id "
        "WHERE e.id = ?",
        (employee_id,),
    )
    row = cur.fetchone()
    conn.close()

    brand_name = row[0] if row else BRAND_NAME
    brand_logo_url = row[1] if row else BRAND_LOGO_URL

    return render_template(
        "feedback.html",
        employee_id=employee_id,
        brand_name=brand_name,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=brand_logo_url,
    )
# SERVER STARTS HERE
@app.route("/good/<employee_id>")
def good(employee_id):

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    # Find the employee's company and its Google review URL
    cur.execute("SELECT company_id FROM employees WHERE id = ?", (employee_id,))
    row = cur.fetchone()
    if not row:
        conn.close()
        return redirect("https://google.com")

    company_id = row[0]
    cur.execute("SELECT google_review_url FROM companies WHERE id = ?", (company_id,))
    company_row = cur.fetchone()
    google_url = company_row[0] if company_row and company_row[0] else "https://google.com"

    cur.execute(
        "UPDATE employees SET scans = scans + 1, good_count = good_count + 1 WHERE id=?",
        (employee_id,)
    )

    # Record a "good" rating so it shows up in the per-employee matrix
    cur.execute(
        "INSERT INTO feedback (company_id, employee_id, rating, comment) VALUES (?, ?, ?, ?)",
        (company_id, employee_id, "good", ""),
    )

    conn.commit()
    conn.close()

    return redirect(google_url)

@app.route("/ok/<employee_id>")
def ok(employee_id):
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute(
        "SELECT c.name, c.logo_url FROM employees e "
        "JOIN companies c ON e.company_id = c.id "
        "WHERE e.id = ?",
        (employee_id,),
    )
    row = cur.fetchone()
    conn.close()

    brand_name = row[0] if row else BRAND_NAME
    brand_logo_url = row[1] if row else BRAND_LOGO_URL

    return render_template(
        "internal_feedback.html",
        employee_id=employee_id,
        rating="ok",
        brand_name=brand_name,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=brand_logo_url,
    )

@app.route("/bad/<employee_id>")
def bad(employee_id):
    conn = sqlite3.connect("database.db")
    cur = conn.cursor()
    cur.execute(
        "SELECT c.name, c.logo_url FROM employees e "
        "JOIN companies c ON e.company_id = c.id "
        "WHERE e.id = ?",
        (employee_id,),
    )
    row = cur.fetchone()
    conn.close()

    brand_name = row[0] if row else BRAND_NAME
    brand_logo_url = row[1] if row else BRAND_LOGO_URL

    return render_template(
        "internal_feedback.html",
        employee_id=employee_id,
        rating="bad",
        brand_name=brand_name,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=brand_logo_url,
    )

@app.route("/submit_internal_feedback", methods=["POST"])
def submit_internal_feedback():

    print("FORM SUBMITTED")   # add this

    employee_id = request.form["employee_id"]
    rating = request.form["rating"]
    comment = request.form["comment"]

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    # Look up company for this employee
    cur.execute("SELECT company_id FROM employees WHERE id = ?", (employee_id,))
    row = cur.fetchone()
    company_id = row[0] if row else 1

    cur.execute(
        "INSERT INTO feedback (company_id, employee_id, rating, comment) VALUES (?, ?, ?, ?)",
        (company_id, employee_id, rating, comment),
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

    company = get_current_company(session.get("user_id"))
    company_id = company["id"] if company else 1

    cur.execute("""
    SELECT feedback.id,
           employees.name,
           feedback.rating,
           feedback.comment,
           feedback.status,
           feedback.created_at
    FROM feedback
    JOIN employees ON feedback.employee_id = employees.id
    WHERE feedback.company_id = ?
    ORDER BY datetime(feedback.created_at) DESC, feedback.id DESC
    """, (company_id,))

    feedback_list = cur.fetchall()

    conn.close()

    return render_template(
        "feedback_list.html",
        feedback=feedback_list,
        brand_name=company["name"] if company else BRAND_NAME,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=company["logo_url"] if company else BRAND_LOGO_URL,
    )


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
    company = get_current_company(session.get("user_id"))
    company_id = company["id"] if company else 1
    cur.execute("""
    SELECT id, name, scans, good_count, ok_count, bad_count
    FROM employees
    WHERE company_id = ?
    ORDER BY scans DESC, name ASC
    """, (company_id,))
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
    company = get_current_company(session.get("user_id"))
    company_id = company["id"] if company else 1
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
    WHERE feedback.company_id = ?
    ORDER BY datetime(feedback.created_at) DESC, feedback.id DESC
    """, (company_id,))
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
        "SELECT id, name, company_id, scans, good_count, ok_count, bad_count FROM employees WHERE id = ?",
        (employee_id,),
    )
    employee = cur.fetchone()

    company_id = employee[2] if employee else 1

    cur.execute(
        "SELECT id, name, scans, good_count, ok_count, bad_count "
        "FROM employees WHERE company_id = ? ORDER BY scans DESC, name ASC",
        (company_id,),
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
        employee=(employee[0], employee[1], employee[3], employee[4], employee[5], employee[6]) if employee else None,
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
    company = get_current_company(session.get("user_id"))
    company_id = company["id"] if company else 1

    # Date range handling
    range_key = request.args.get("range", "30d")
    range_map_days = {
        "7d": 7,
        "30d": 30,
        "6m": 180,
        "1y": 365,
    }
    days = range_map_days.get(range_key)
    start_date = None
    if days is not None:
        start_date = (datetime.utcnow() - timedelta(days=days)).date().isoformat()

    # Daily breakdown
    if start_date:
        cur.execute("""
        SELECT DATE(created_at) as day,
               COUNT(*) as total,
               SUM(CASE WHEN rating = 'good' THEN 1 ELSE 0 END) as good_count,
               SUM(CASE WHEN rating = 'ok' THEN 1 ELSE 0 END) as ok_count,
               SUM(CASE WHEN rating = 'bad' THEN 1 ELSE 0 END) as bad_count
        FROM feedback
        WHERE company_id = ? AND DATE(created_at) >= ?
        GROUP BY day
        ORDER BY day ASC
        """, (company_id, start_date))
    else:
        cur.execute("""
        SELECT DATE(created_at) as day,
               COUNT(*) as total,
               SUM(CASE WHEN rating = 'good' THEN 1 ELSE 0 END) as good_count,
               SUM(CASE WHEN rating = 'ok' THEN 1 ELSE 0 END) as ok_count,
               SUM(CASE WHEN rating = 'bad' THEN 1 ELSE 0 END) as bad_count
        FROM feedback
        WHERE company_id = ?
        GROUP BY day
        ORDER BY day ASC
        """, (company_id,))
    daily_stats = cur.fetchall()

    # Per-employee in selected range
    if start_date:
        cur.execute("""
        SELECT e.id,
               e.name,
               COALESCE(SUM(CASE WHEN f.rating = 'good' THEN 1 ELSE 0 END), 0) as good_cnt,
               COALESCE(SUM(CASE WHEN f.rating = 'ok' THEN 1 ELSE 0 END), 0) as ok_cnt,
               COALESCE(SUM(CASE WHEN f.rating = 'bad' THEN 1 ELSE 0 END), 0) as bad_cnt
        FROM employees e
        LEFT JOIN feedback f
          ON f.employee_id = e.id
         AND f.company_id = ?
         AND DATE(f.created_at) >= ?
        WHERE e.company_id = ?
        GROUP BY e.id, e.name
        ORDER BY (good_cnt + ok_cnt + bad_cnt) DESC, e.name ASC
        """, (company_id, start_date, company_id))
    else:
        cur.execute("""
        SELECT e.id,
               e.name,
               COALESCE(SUM(CASE WHEN f.rating = 'good' THEN 1 ELSE 0 END), 0) as good_cnt,
               COALESCE(SUM(CASE WHEN f.rating = 'ok' THEN 1 ELSE 0 END), 0) as ok_cnt,
               COALESCE(SUM(CASE WHEN f.rating = 'bad' THEN 1 ELSE 0 END), 0) as bad_cnt
        FROM employees e
        LEFT JOIN feedback f
          ON f.employee_id = e.id
         AND f.company_id = ?
        WHERE e.company_id = ?
        GROUP BY e.id, e.name
        ORDER BY (good_cnt + ok_cnt + bad_cnt) DESC, e.name ASC
        """, (company_id, company_id))
    per_employee_30d = cur.fetchall()

    conn.close()

    return render_template(
        "analytics.html",
        daily_stats=daily_stats,
        per_employee_30d=per_employee_30d,
        selected_range=range_key,
        brand_name=company["name"] if company else BRAND_NAME,
        brand_tagline=BRAND_TAGLINE,
        brand_logo_url=company["logo_url"] if company else BRAND_LOGO_URL,
    )

@app.route("/thankyou")
def thankyou():
    return render_template("thankyou.html", brand_name=BRAND_NAME, brand_tagline=BRAND_TAGLINE, brand_logo_url=BRAND_LOGO_URL)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
