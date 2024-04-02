import { BrowserRouter, Route, Routes } from "react-router-dom";
import { StoreFile } from "./components/StoreFile";
import "./App.css";
import { UpdateFile } from "./components/UpdateFile";
import { UpdateFileContent } from "./components/UpdateFileContent";

function App() {
    return (
        <>
            <BrowserRouter>
                <Routes>
                    <Route path="/" element={<StoreFile />} />
                    <Route path="/update" element={<UpdateFile />} />
                    <Route
                        path="/update-content"
                        element={<UpdateFileContent />}
                    />
                </Routes>
            </BrowserRouter>
        </>
    );
}

export default App;
