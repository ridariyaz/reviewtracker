from flask import Flask, render_template, request, redirect, url_for
import sqlite3
import qrcode

app = Flask(__name__)

conn = sqlite3.connect("database.db")
cur = conn.cursor()

cur.execute("""
CREATE TABLE IF NOT EXISTS feedback (
id INTEGER PRIMARY KEY AUTOINCREMENT,
employee_id INTEGER,
rating TEXT,
comment TEXT
)
""")

conn.commit()
conn.close()

@app.route("/")
def home():
    return "Review system running"


@app.route("/admin")
def admin():

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute("SELECT * FROM employees")
    employees = cur.fetchall()

    conn.close()

    return render_template("admin.html", employees=employees)

@app.route("/add_employee", methods=["POST"])
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

    img = qrcode.make(url)
    img.save(f"static/qrcodes/{employee_id}.png")

    return redirect("/admin")

@app.route("/review/<employee_id>")
def review(employee_id):
    return render_template("feedback.html", employee_id=employee_id)
# SERVER STARTS HERE
@app.route("/good/<employee_id>")
def good(employee_id):

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

    cur.execute(
        "UPDATE employees SET scans = scans + 1 WHERE id=?",
        (employee_id,)
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

    conn.commit()
    conn.close()

    return redirect("/thankyou")

@app.route("/feedback")
def feedback():

    conn = sqlite3.connect("database.db")
    cur = conn.cursor()

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
