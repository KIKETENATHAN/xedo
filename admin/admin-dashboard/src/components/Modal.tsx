import React from 'react';

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    children: React.ReactNode;
}

const Modal: React.FC<ModalProps> = ({ isOpen, onClose, title, children }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 flex items-center justify-center z-50">
            <div className="absolute inset-0 bg-black opacity-50" onClick={onClose}></div>
            <div className="bg-white rounded-lg shadow-lg z-10 w-11/12 md:w-1/3">
                <div className="p-4 border-b">
                    <h2 className="text-lg font-semibold">{title}</h2>
                </div>
                <div className="p-4">
                    {children}
                </div>
                <div className="p-4 border-t">
                    <button 
                        className="bg-blue-500 text-white px-4 py-2 rounded" 
                        onClick={onClose}
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Modal;