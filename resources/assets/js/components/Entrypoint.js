import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import Header from './layout/Header';
import FundingIndex from './funding/index';

export default class Entrypoint extends Component {
    render() {
        return (
            <div>
                <Header/>
                <FundingIndex/>
            </div>
        );
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<Entrypoint />, document.getElementById('app'));
}
