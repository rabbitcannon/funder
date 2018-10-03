import React, {Component} from "react";

import FundingIndex from '../funding/Index';

class DepositsIndex extends Component {


    render() {
        return (
            <div className="animated fadeIn">
                <FundingIndex/>
            </div>
        );
    }
}

export default DepositsIndex;