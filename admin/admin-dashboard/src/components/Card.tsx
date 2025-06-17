import React from 'react';

interface CardProps {
    title: string;
    value: number;
    icon: React.ReactNode;
}

const Card: React.FC<CardProps> = ({ title, value, icon }) => {
    return (
        <div className="bg-white shadow-md rounded-lg p-4 flex items-center justify-between">
            <div className="flex items-center">
                <div className="text-3xl text-blue-500">{icon}</div>
                <div className="ml-4">
                    <h2 className="text-lg font-semibold">{title}</h2>
                    <p className="text-gray-600">{value}</p>
                </div>
            </div>
        </div>
    );
};

export default Card;