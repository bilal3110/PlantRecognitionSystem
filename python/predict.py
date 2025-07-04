import sys
import json
import os
import tensorflow as tf
import numpy as np
import pickle
import logging
import warnings

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
tf.get_logger().setLevel(logging.ERROR)
warnings.filterwarnings('ignore')

try:
    script_dir = os.path.dirname(os.path.abspath(__file__))
    model_path = os.path.join(script_dir, "plant_recognition_model.h5")
    label_encoder_path = os.path.join(script_dir, "class_indices.pkl") 
    image_path = sys.argv[1]

    if not os.path.exists(image_path):
        raise FileNotFoundError(f"Image file not found: {image_path}")
    if not os.path.exists(model_path):
        raise FileNotFoundError(f"Model file not found: {model_path}")
    if not os.path.exists(label_encoder_path):
        raise FileNotFoundError(f"Label encoder file not found: {label_encoder_path}")

    model = tf.keras.models.load_model(model_path)
    with open(label_encoder_path, "rb") as f:
        class_indices = pickle.load(f)

    index_to_class = {v: k for k, v in class_indices.items()}

    image = tf.keras.preprocessing.image.load_img(image_path, target_size=(224, 224))
    image_array = tf.keras.preprocessing.image.img_to_array(image)
    image_array = tf.keras.applications.mobilenet_v2.preprocess_input(image_array)
    image_array = np.expand_dims(image_array, axis=0)

    predictions = model.predict(image_array, verbose=0)

    top_3_indices = np.argsort(predictions[0])[-3:][::-1]
    top_3_predictions = [
        {
            "common_name": index_to_class[i],
            "confidence": float(predictions[0][i])
        }
        for i in top_3_indices
    ]

    result = {
        "common_name": top_3_predictions[0]["common_name"],
        "confidence": top_3_predictions[0]["confidence"],
        "top_3": top_3_predictions
    }

    print(json.dumps(result))

except Exception as e:
    print(json.dumps({"error": str(e)}))
