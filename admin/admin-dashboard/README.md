# Admin Dashboard

This project is an Admin Dashboard built with React and TypeScript, utilizing Tailwind CSS for styling. The dashboard provides an interface for managing drivers, passengers, saccos, vehicles, transactions, and trips.

## Features

- **Landing Page**: Displays summary cards for drivers, passengers, saccos, vehicles, transactions, and trips.
- **Sidebar Navigation**: Easy navigation to different sections of the dashboard including Drivers, Passengers, Saccos, Trips, etc.
- **Dynamic Modals**: Add new entries for drivers, saccos, and trips through modal pop-ups.
- **Well-Formatted Tables**: Each section features a well-structured table to display relevant data.

## Project Structure

```
admin-dashboard
├── src
│   ├── components
│   │   ├── Aside.tsx
│   │   ├── Card.tsx
│   │   ├── Dashboard.tsx
│   │   ├── DriversTable.tsx
│   │   ├── PassengersTable.tsx
│   │   ├── SaccosTable.tsx
│   │   ├── VehiclesTable.tsx
│   │   ├── TransactionsTable.tsx
│   │   ├── TripsTable.tsx
│   │   └── Modal.tsx
│   ├── pages
│   │   ├── Landing.tsx
│   │   ├── Drivers.tsx
│   │   ├── Passengers.tsx
│   │   ├── Saccos.tsx
│   │   ├── Vehicles.tsx
│   │   ├── Transactions.tsx
│   │   └── Trips.tsx
│   ├── types
│   │   └── index.ts
│   ├── App.tsx
│   └── index.tsx
├── tailwind.config.js
├── package.json
├── tsconfig.json
└── README.md
```

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   cd admin-dashboard
   ```

2. Install dependencies:
   ```
   npm install
   ```

3. Start the development server:
   ```
   npm start
   ```

## Usage

- Navigate through the sidebar to access different sections of the dashboard.
- Use the modal pop-ups to add new drivers, saccos, and trips.
- View summaries and detailed tables for each category.

## Technologies Used

- React
- TypeScript
- Tailwind CSS

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License

This project is licensed under the MIT License.