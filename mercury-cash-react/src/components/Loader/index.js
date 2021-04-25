import React from 'react';
import './styles.scss'

export const Loader = ({ children }) => {
  return (
    <div className="mercury-cash__loader">
      { children }
    </div>
  );
};