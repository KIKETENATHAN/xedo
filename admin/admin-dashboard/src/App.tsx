import React from 'react';
import { BrowserRouter as Router, Route, Switch } from 'react-router-dom';
import Dashboard from './components/Dashboard';
import Drivers from './pages/Drivers';
import Passengers from './pages/Passengers';
import Saccos from './pages/Saccos';
import Vehicles from './pages/Vehicles';
import Transactions from './pages/Transactions';
import Trips from './pages/Trips';

const App: React.FC = () => {
  return (
    <Router>
      <Switch>
        <Route path="/" exact component={Dashboard} />
        <Route path="/drivers" component={Drivers} />
        <Route path="/passengers" component={Passengers} />
        <Route path="/saccos" component={Saccos} />
        <Route path="/vehicles" component={Vehicles} />
        <Route path="/transactions" component={Transactions} />
        <Route path="/trips" component={Trips} />
      </Switch>
    </Router>
  );
};

export default App;