import React from 'react'
import './App.css'
import EntitiesList from './EAV/EntitiesList'

function App() {
  return (
    <div className="App">
      <header className="App-header">
        <p>
          Hello Hawaii.
        </p>
        <EntitiesList />
      </header>
    </div>
  );
}

export default App;
