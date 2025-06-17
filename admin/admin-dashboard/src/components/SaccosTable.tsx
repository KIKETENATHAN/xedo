import React, { useState } from 'react';
import Modal from './Modal';

const SaccosTable = () => {
    const [saccos, setSaccos] = useState([]);
    const [modalOpen, setModalOpen] = useState(false);
    const [newSacco, setNewSacco] = useState({ name: '', location: '' });

    const handleAddSacco = () => {
        setSaccos([...saccos, newSacco]);
        setNewSacco({ name: '', location: '' });
        setModalOpen(false);
    };

    return (
        <div className="p-4">
            <h2 className="text-xl font-bold mb-4">Saccos</h2>
            <button
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded"
                onClick={() => setModalOpen(true)}
            >
                Add Sacco
            </button>
            <table className="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th className="py-2 px-4 border-b">Name</th>
                        <th className="py-2 px-4 border-b">Location</th>
                    </tr>
                </thead>
                <tbody>
                    {saccos.map((sacco, index) => (
                        <tr key={index}>
                            <td className="py-2 px-4 border-b">{sacco.name}</td>
                            <td className="py-2 px-4 border-b">{sacco.location}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            {modalOpen && (
                <Modal onClose={() => setModalOpen(false)}>
                    <h2 className="text-lg font-bold mb-4">Add New Sacco</h2>
                    <div className="mb-4">
                        <label className="block mb-2">Name</label>
                        <input
                            type="text"
                            value={newSacco.name}
                            onChange={(e) => setNewSacco({ ...newSacco, name: e.target.value })}
                            className="border border-gray-300 p-2 w-full"
                        />
                    </div>
                    <div className="mb-4">
                        <label className="block mb-2">Location</label>
                        <input
                            type="text"
                            value={newSacco.location}
                            onChange={(e) => setNewSacco({ ...newSacco, location: e.target.value })}
                            className="border border-gray-300 p-2 w-full"
                        />
                    </div>
                    <button
                        className="px-4 py-2 bg-green-500 text-white rounded"
                        onClick={handleAddSacco}
                    >
                        Add Sacco
                    </button>
                </Modal>
            )}
        </div>
    );
};

export default SaccosTable;