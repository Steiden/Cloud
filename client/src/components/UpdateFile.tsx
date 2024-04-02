import axios from "axios";
import { FormEvent, useState } from "react";

export const UpdateFile = () => {
    const [fileId, setFileId] = useState<number>(0);
    const [name, setName] = useState<string>("");
    const [uri, setUri] = useState<string>("");

    const handleSubmit = async (
        e: FormEvent<HTMLFormElement>
    ): Promise<void> => {
        e.preventDefault();

        // Create data to fetch
        const dataToFetch: Record<string, FileList | string | null> = {};

        if (name) dataToFetch["name"] = name;
        if (uri) dataToFetch["uri"] = uri;

        // Send request
        const response = await axios.post(
            `http://127.0.0.1:8000/api/files/${fileId}`,
            dataToFetch,
            {
                headers: {
                    "Content-type": "application/json",
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
                <label htmlFor="fileId" className="file-form__label">
                    File's id to update:
                </label>
                <input
                    type="number"
                    name="fileId"
                    id="fileId"
                    className=""
                    value={fileId}
                    onChange={(e) => setFileId(Number(e.target.value))}
                />
            </div>
            <div className="file-form__input-container">
                <label htmlFor="name" className="file-form__label">
                    File's name to update (not required):
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    className="file-form__input"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                />
            </div>
            <div className="file-form__input-container">
                <label htmlFor="uri" className="file-form__label">
                    File's uri to update (not required):
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
                Send for update
            </button>
        </form>
    );
};
