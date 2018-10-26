import React, {Component} from "react";

import FundingIndex from '../funding/Index';

class DepositsIndex extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className="animated fadeIn">
                <FundingIndex balance={this.props.balance} />
            </div>
        );
    }
}

export default DepositsIndex;