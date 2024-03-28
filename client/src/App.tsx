import { FormEvent, useState } from "react";
import "./App.css";
import axios, { AxiosResponse } from "axios";

function App() {
    const [files, setFiles] = useState<FileList | null>({} as FileList);

    const handleSubmit = async (e: FormEvent<HTMLFormElement>): Promise<void> => {
        e.preventDefault();

        const fetchStart: number = Date.now();

        const response: Promise<AxiosResponse> = axios.post("http://127.0.0.1:8000/api/files", { files }, 
            { headers: { "Content-Type": "multipart/form-data", "Authorization": `Bearer ${localStorage.getItem("token")}` } });
        
        console.log((await response).data);

        console.log(`Took ${Date.now() - fetchStart}ms to fetch files`);
    };

    return (
        <>
            <form onSubmit={handleSubmit}>
                <label htmlFor="files">Files</label>
                <input type="file" name="files" id="files" multiple onChange={(e) => setFiles(e.target.files)} />
                <button type="submit">Submit</button>
            </form>
        </>
    );
}

export default App;
