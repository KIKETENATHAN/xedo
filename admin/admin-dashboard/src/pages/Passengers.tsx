import React from 'react';
import PassengersTable from '../components/PassengersTable';
import Modal from '../components/Modal';

const Passengers: React.FC = () => {
    const [isModalOpen, setIsModalOpen] = React.useState(false);

    const handleOpenModal = () => {
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
    };

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">Passengers</h1>
            <button 
                onClick={handleOpenModal} 
                className="mb-4 px-4 py-2 bg-blue-500 text-white rounded"
            >
                Add Passenger
            </button>
            <PassengersTable />
            {isModalOpen && (
                <Modal onClose={handleCloseModal}>
                    {/* Add Passenger Form goes here */}
                    <h2 className="text-xl font-semibold mb-4">Add New Passenger</h2>
                    {/* Form fields for adding a passenger */}
                    <form>
                        <div className="mb-4">
                            <label className="block text-gray-700">Name</label>
                            <input type="text" className="mt-1 block w-full border rounded p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block text-gray-700">Email</label>
                            <input type="email" className="mt-1 block w-full border rounded p-2" />
                        </div>
                        <div className="mb-4">
                            <label className="block text-gray-700">Phone</label>
                            <input type="text" className="mt-1 block w-full border rounded p-2" />
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

export default Passengers;