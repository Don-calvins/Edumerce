import React, { useState } from 'react';
import { uploadAssignmentFile } from '../api/upload_service';

interface Props {
  contractId: number;
  onSuccess: () => void;
}

const FileUploadComponent: React.FC<Props> = ({ contractId, onSuccess }) => {
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);

  const handleUpload = async () => {
    if (!selectedFile) return;
    
    setUploading(true);
    try {
      await uploadAssignmentFile(contractId, selectedFile);
      alert("File uploaded! Student notified to release final 50%.");
      onSuccess();
    } catch (err) {
      alert("Error uploading file.");
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="mt-4 p-4 border-2 border-dashed border-gray-300 rounded">
      <input 
        type="file" 
        onChange={(e) => setSelectedFile(e.target.files ? e.target.files[0] : null)}
        className="mb-2"
      />
      <button 
        onClick={handleUpload}
        disabled={!selectedFile || uploading}
        className={`px-4 py-2 rounded text-white ${uploading ? 'bg-gray-400' : 'bg-blue-600'}`}
      >
        {uploading ? 'Uploading...' : 'Submit Final Work'}
      </button>
    </div>
  );
};

export default FileUploadComponent;
