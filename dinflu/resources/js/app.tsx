import React from 'react';
import ReactDOM from 'react-dom/client';
import HelloWorld from './components/HelloWorld';

const App = () => {
    return (
        <>
            <HelloWorld name="Mundo" />
        </>
    );
};

// Certifique-se de que o elemento com o ID 'app' existe no seu HTML
const rootElement = document.getElementById('app');

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement as HTMLElement);
    root.render(<App />);
}
