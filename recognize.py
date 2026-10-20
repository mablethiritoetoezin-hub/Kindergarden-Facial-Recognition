import face_recognition
import pickle
import sys
import pathlib
import os

def recognize_face(image_path, model_path):
    with open(model_path, 'rb') as f:
        known_encodings, known_ids = pickle.load(f)

    image = face_recognition.load_image_file(image_path)
    encodings = face_recognition.face_encodings(image)
    if not encodings:
        return None

    encoding = encodings[0]
    matches = face_recognition.compare_faces(known_encodings, encoding)
    if True in matches:
        first_match_index = matches.index(True)
        return known_ids[first_match_index]
    return None

if __name__ == "__main__":
    image_path = sys.argv[1]
    BASE_DIR = pathlib.Path(__file__).parent.resolve()
    model_path = os.path.join(BASE_DIR, "storage", "app", "model.pkl")
    child_id = recognize_face(image_path, model_path)
    print(child_id if child_id else "No match found")