import axios, { AxiosResponse } from "axios";
import { FormEvent, useState } from "react";

export const StoreFile = () => {
    const [files, setFiles] = useState<FileList | null>({} as FileList);
    const [uri, setUri] = useState<string>("");

    const handleSubmit = async (
        e: FormEvent<HTMLFormElement>
    ): Promise<void> => {
        e.preventDefault();

        const dataToFetch: Record<string, FileList | string | null> = {
            files: files,
        };

        if (uri) dataToFetch["current_dir"] = uri;

        console.log(dataToFetch);

        const response = await axios.post(
            "http://127.0.0.1:8000/api/files",
            dataToFetch,
            {
                headers: {
                    "Content-type": "multipart/form-data",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            }
        );

        if ((await response).status !== 200)
            throw new Error((await response).statusText);

        const data = (await response).data;
        console.log(data);
    };

    return (
        <form onSubmit={handleSubmit} className="file-form">
            <div className="file-form__input-container">
                <label htmlFor="files" className="file-form__label">
                    Files to send:
                </label>
                <input
                    type="file"
                    name="files"
                    id="files"
                    className=""
                    multiple
                    onChange={(e) => setFiles(e.target.files)}
                />
            </div>
            <div className="file-form__input-container">
                <label htmlFor="files" className="file-form__label">
                    Uri (not required):
                </label>
                <input
                    type="text"
                    name="uri"
                    id="uri"
                    className="file-form__input"
                    value={uri}
                    onChange={(e) => setUri(e.target.value)}
                />
            </div>
            <button type="submit" className="file-form__button">
                Send files
            </button>
        </form>
    );
};
