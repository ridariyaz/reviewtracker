FROM python:3.13-slim

WORKDIR /app

# Install system dependencies:
# - libpq-dev for Postgres clients
# - build-essential for compiling wheels if needed
# - image libs for Pillow (JPEG/PNG/zlib)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev build-essential \
    libjpeg-dev zlib1g-dev libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# Install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY . .

ENV PORT=8080

CMD ["sh", "-c", "gunicorn app:app --bind 0.0.0.0:$PORT"]

