import os
import tensorflow as tf
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Dropout, GlobalAveragePooling2D
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.callbacks import EarlyStopping
import numpy as np
import pickle
from sklearn.preprocessing import LabelEncoder
from sklearn.utils.class_weight import compute_class_weight

try:
    dataset_dir = 'C:/xampp/htdocs/PlantReco/dataset'
    model_path_h5 = 'C:/xampp/htdocs/PlantReco/python/plant_recognition_model.h5'
    model_path_keras = 'C:/xampp/htdocs/PlantReco/python/plant_recognition_model.keras'
    label_encoder_path = 'C:/xampp/htdocs/PlantReco/python/label_encoder.pkl'
    
    img_height, img_width = 224, 224  
    batch_size = 32
    
    datagen = ImageDataGenerator(
        rescale=1./255,
        rotation_range=30,
        width_shift_range=0.2,
        height_shift_range=0.2,
        shear_range=0.2,
        zoom_range=0.2,
        horizontal_flip=True,
        brightness_range=[0.8, 1.2],
        validation_split=0.2,
        preprocessing_function=tf.keras.applications.mobilenet_v2.preprocess_input
    )
    
    train_generator = datagen.flow_from_directory(
        dataset_dir,
        target_size=(img_height, img_width),
        batch_size=batch_size,
        class_mode='categorical',
        subset='training',
        shuffle=True
    )
    
    validation_generator = datagen.flow_from_directory(
        dataset_dir,
        target_size=(img_height, img_width),
        batch_size=batch_size,
        class_mode='categorical',
        subset='validation',
        shuffle=True
    )
    
    label_encoder = LabelEncoder()
    label_encoder.fit(list(train_generator.class_indices.keys()))
    with open(label_encoder_path, 'wb') as f:
        pickle.dump(label_encoder, f)
    
    base_model = MobileNetV2(
        input_shape=(img_height, img_width, 3), 
        include_top=False, 
        weights='imagenet'
    )
    
    base_model.trainable = True
    for layer in base_model.layers[:-100]: 
        layer.trainable = False
    
    model = Sequential([
        base_model,
        GlobalAveragePooling2D(),
        Dense(512, activation='relu'),
        Dropout(0.5),
        Dense(len(train_generator.class_indices), activation='softmax')
    ])
    
    class_weights = compute_class_weight(
        'balanced', 
        classes=np.unique(train_generator.classes), 
        y=train_generator.classes
    )
    class_weights = dict(enumerate(class_weights))
    
    model.compile(
        optimizer=tf.keras.optimizers.Adam(learning_rate=3e-5), 
        loss='categorical_crossentropy',
        metrics=['accuracy']
    )
    
    early_stopping = EarlyStopping(
        monitor='val_loss',  
        patience=15,      
        restore_best_weights=True
    )
    
    history = model.fit(
        train_generator,
        epochs=100,
        validation_data=validation_generator,
        callbacks=[early_stopping],
        class_weight=class_weights 
    )
    
    model.save(model_path_h5)
    model.save(model_path_keras)
    print(f"Model saved to {model_path_h5} and {model_path_keras}")
    
except Exception as e:
    print(f"Error: {str(e)}")