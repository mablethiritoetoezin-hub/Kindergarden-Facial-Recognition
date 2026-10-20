import face_recognition
import os
import pickle
import pathlib

def train_model(training_dir, model_path):
    known_encodings = []
    known_ids = []

    for child_id in os.listdir(training_dir):
        child_dir = os.path.join(training_dir, child_id)
        if os.path.isdir(child_dir):
            print(f"Processing child_id: {child_id}")
            for image_file in os.listdir(child_dir):
                image_path = os.path.join(child_dir, image_file)
                print(f"Loading image: {image_path}")
                image = face_recognition.load_image_file(image_path)
                encodings = face_recognition.face_encodings(image)
                if encodings:
                    known_encodings.append(encodings[0])
                    known_ids.append(int(child_id))
                    print(f"Encoding found for {image_path}")
                else:
                    print(f"No face detected in {image_path}")

    if not known_encodings:
        print("Warning: No face encodings generated!")
    else:
        print(f"Generated {len(known_encodings)} encodings for {len(set(known_ids))} children")

    with open(model_path, 'wb') as f:
        pickle.dump((known_encodings, known_ids), f)

if __name__ == "__main__":
    base_dir = pathlib.Path(__file__).parent.resolve()
    training_dir = str(base_dir / "storage/app/training_faces")
    model_path = str(base_dir / "storage/app/model.pkl")
    train_model(training_dir, model_path)
    print("Model trained successfully")