import React, { useState } from 'react';
import SaccosTable from '../components/SaccosTable';
import Modal from '../components/Modal';

const Saccos = () => {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const handleOpenModal = () => {
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Saccos Management</h1>
            <button 
                onClick={handleOpenModal} 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded"
            >
                Add Sacco
            </button>
            <SaccosTable />
            {isModalOpen && (
                <Modal onClose={handleCloseModal}>
                    {/* Add your form for adding a new Sacco here */}
                    <h2 className="text-xl font-semibold mb-4">Add New Sacco</h2>
                    <form>
                        <div className="mb-4">
                            <label className="block text-gray-700">Sacco Name</label>
                            <input 
                                type="text" 
                                className="mt-1 block w-full border border-gray-300 rounded p-2" 
                                placeholder="Enter Sacco Name" 
                            />
                        </div>
                        <div className="mb-4">
                            <label className="block text-gray-700">Sacco Code</label>
                            <input 
                                type="text" 
                                className="mt-1 block w-full border border-gray-300 rounded p-2" 
                                placeholder="Enter Sacco Code" 
                            />
                        </div>
                        <button 
                            type="submit" 
                            className="px-4 py-2 bg-green-500 text-white rounded"
                        >
                            Submit
                        </button>
                    </form>
                </Modal>
            )}
        </div>
    );
};

export default Saccos;