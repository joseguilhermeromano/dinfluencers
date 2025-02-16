import React from 'react';

// Definindo a interface para as props
interface HelloWorldProps {
  name: string;
}

const HelloWorld = ({ name }: HelloWorldProps) => {
  return <h1>Olá, {name}!</h1>;
};

export default HelloWorld;
