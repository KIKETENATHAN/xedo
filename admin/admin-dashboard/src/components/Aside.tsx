import React from 'react';
import { Link } from 'react-router-dom';

const Aside: React.FC = () => {
    return (
        <aside className="w-64 h-full bg-gray-800 text-white">
            <div className="p-4">
                <h2 className="text-lg font-bold">Admin Dashboard</h2>
            </div>
            <nav className="mt-4">
                <ul>
                    <li className="hover:bg-gray-700">
                        <Link to="/drivers" className="block p-4">Drivers</Link>
                    </li>
                    <li className="hover:bg-gray-700">
                        <Link to="/passengers" className="block p-4">Passengers</Link>
                    </li>
                    <li className="hover:bg-gray-700">
                        <Link to="/saccos" className="block p-4">Saccos</Link>
                    </li>
                    <li className="hover:bg-gray-700">
                        <Link to="/trips" className="block p-4">Trips</Link>
                    </li>
                    <li className="hover:bg-gray-700">
                        <Link to="/vehicles" className="block p-4">Vehicles</Link>
                    </li>
                    <li className="hover:bg-gray-700">
                        <Link to="/transactions" className="block p-4">Transactions</Link>
                    </li>
                </ul>
            </nav>
        </aside>
    );
};

export default Aside;