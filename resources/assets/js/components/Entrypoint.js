import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Entrypoint extends Component {
    render() {
        return (
            <div className="container">
                <div className="row">
                    <div className="large-12">
                        Landing component
                    </div>
                </div>
            </div>
        );
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<Entrypoint />, document.getElementById('app'));
}
