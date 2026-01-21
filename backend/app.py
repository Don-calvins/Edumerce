from flask import Flask, request, jsonify
from assignment_service import AssignmentService
from models import db_session

app = Flask(__name__)

@app.route('/assignments', methods=['POST'])
def create_assignment():
    data = request.json
    # Extract user_id from the JWT token (we discussed this in Auth)
    student_id = 123 
    
    service = AssignmentService(db_session)
    new_job = service.post_job(
        student_id=student_id,
        title=data['title'],
        description=data['description']
    )
    
    return jsonify({"status": "success", "id": new_job.id}), 201
