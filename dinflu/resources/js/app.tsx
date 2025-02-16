import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom/client';

import Home from './components/home';

ReactDOM.createRoot(document.getElementById('app') as HTMLElement).render(
    <Home />
);

