# 🌿 Plant Recognition System

A web-based system that allows users to upload a plant image and identifies the plant species using a trained machine learning model.

---

## 📌 Features

- 🌱 Upload image of a plant
- 🤖 Predict plant species using a pre-trained model
- 💻 Clean and responsive web interface
- 🔒 Secure backend powered by PHP
- 🐍 Python integration for model inference
- 🧠 Supports 5–7 different plant types

---

## 📁 Project Structure


PlantRecoSystem/
├── index.php # Main interface
├── upload/ # Folder to store uploaded images
├── python/
│ ├── predict.py # Python script for prediction
│ ├── plant_recognition_model.h5 (NOT INCLUDED)
│ └── class_indices.pkl (NOT INCLUDED)
├── venv/ # Python virtual environment (Not included)
├── .gitignore
└── README.md


---

## ⚙️ Requirements

### PHP Requirements:
- PHP 7.4 or higher
- XAMPP or similar local server (Apache + PHP)

### Python Requirements:
- Python 3.9 (recommended)
- pip
- Virtual environment (`venv`)

---

🧠 2. Download Pre-trained Model & Class File
The trained model and class index file are not included in the repo due to size limitations.
 Download them from the link below and place them in the python/ directory:
https://drive.google.com/drive/folders/1Tn3zVV02MfMvI1Wo_aqZvRUjpd2Heqmw?usp=sharing
Files you’ll get:
plant_recognition_model.h5


class_indices.pkl

## 🚀 Setup Guide

### 🔧 1. Clone the Repository
```bash
git clone https://github.com/yourusername/PlantRecoSystem.git
cd PlantRecoSystem
🐍 3. Set Up Python Environment

python -m venv venv
venv\Scripts\activate         # On Windows
pip install -r requirements.txt

If you don’t have a requirements.txt, manually install:
pip install tensorflow pillow numpy

🌐 4. Run the App
Start Apache via XAMPP


Open your browser and go to:
 http://localhost/PlantRecoSystem/
📸 How It Works
User uploads a plant image.


The image is saved in the upload/ folder.


PHP executes the Python script with the image path.


Python loads the trained model and predicts the plant class.


The predicted name is displayed on the webpage.
🤝 Credits
Developed by Bilal Aqeel
 Model trained using TensorFlow and Python.

📬 Contact
If you’d like to contribute, report bugs, or request improvements:
 📧 bilalaqeel.dev@gmail.com

📝 License
This project is for educational/demo purposes only.
 Not for commercial use without permission.




