import axios from "axios";
import { EventHandler, FormEvent, useState } from "react";

export const UpdateFileContent = () => {
    const [fileId, setFileId] = useState<number>(0);
    const [file, setFile] = useState<File>({} as File);

    const handleSubmit: EventHandler<FormEvent> = async (
        e: FormEvent<HTMLFormElement>
    ): Promise<void> => {
        e.preventDefault();

        const response = await axios.post(
            `http://127.0.0.1:8000/api/files/${fileId}/content`,
            {
                file: file
            },
            {
                headers: {
                    "Content-type": "multipart/form-data",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            }
        );

        if (response.status !== 200) throw new Error(response.statusText);

        const data = response.data;
        console.log(data);
    };

    return (
        <form onSubmit={handleSubmit} className="file-form">
            <div className="file-form__input-container">
                <label htmlFor="fileId" className="file-form__label">
                    File's id to update:
                </label>
                <input
                    type="number"
                    name="fileId"
                    id="fileId"
                    className="file-form__input"
                    value={fileId}
                    onChange={(e) => setFileId(Number(e.target.value))}
                />
            </div>
            <div className="file-form__input-container">
                <label htmlFor="fileId" className="file-form__label">
                    File's content to update:
                </label>
                <input
                    type="file"
                    name="file"
                    id="file"
                    className=""
                    onChange={(e) => setFile(e.target.files![0])}
                />
            </div>
            <button type="submit" className="file-form__button">
                Send for update
            </button>
        </form>
    );
};
