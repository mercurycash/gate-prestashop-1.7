import React from 'react';
import classNames from "classnames";
import PropTypes from "prop-types";
import './styles.scss'


/**
 * Title component
 **/
export const Title = ({ children, large, small, lighter }) => {
  const classes = classNames(
    'mercury-cash__title',
    { 'mercury-cash__title--big': large },
    { 'mercury-cash__title--small': small },
    { 'mercury-cash__title--lighter': lighter },
  )

  return (
    <h3 className={ classes }>
      { children }
    </h3>
  );
};

Title.propTypes = {
  large: PropTypes.bool,
  small: PropTypes.bool,
  lighter: PropTypes.bool,
}

Title.defaultProps = {
  large: false,
  small: false,
  lighter: false
}