import jwt
import datetime
from werkzeug.security import generate_password_hash, check_password_hash
from models import User # Import the User model we created

SECRET_KEY = "edumerce_secret_key_123" # In a real app, use an environment variable

class AuthService:
    def __init__(self, db_session):
        self.db = db_session

    def register_user(self, email, password, role):
        """Hashes password and saves new user to the database."""
        hashed_pw = generate_password_hash(password, method='pbkdf2:sha256')
        new_user = User(
            email=email, 
            password_hash=hashed_pw, 
            role=role
        )
        self.db.add(new_user)
        self.db.commit()
        return {"message": f"User {email} registered as {role}."}

    def login_user(self, email, password):
        """Checks credentials and returns a JWT token with the user's role."""
        user = self.db.query(User).filter_by(email=email).first()
        
        if user and check_password_hash(user.password_hash, password):
            # Create a token that expires in 24 hours
            token = jwt.encode({
                'sub': user.id,
                'role': user.role,
                'exp': datetime.datetime.utcnow() + datetime.timedelta(hours=24)
            }, SECRET_KEY, algorithm="HS256")
            
            return {"token": token, "role": user.role}
        
        return {"error": "Invalid email or password"}
