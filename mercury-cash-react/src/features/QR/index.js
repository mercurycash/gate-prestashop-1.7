import React from 'react';
import { renderToStaticMarkup } from "react-dom/server";
import './styles.scss'

import { QRCode } from 'react-qrcode-logo';

// import {ReactComponent as QRLogo} from '../../assets/images/logos/qr-logo.svg'
import {ReactComponent as QRLogoSmallBG} from '../../assets/images/logos/qr-logo-small-background.svg'

const svgString = encodeURIComponent(renderToStaticMarkup(<QRLogoSmallBG />));
const QRLogoURI = `data:image/svg+xml,${svgString}`;


export const QR = ({ value = 'https://google.com' }) => {
  return (
    <div className="mercury-cash__qrCode">
      <QRCode
        value={ value }
        size={ 240 }
        logoImage={ QRLogoURI }
      />
    </div>
  );
};