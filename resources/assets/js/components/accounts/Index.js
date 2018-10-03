import React, {Component} from "react";

import Accounts from './Accounts';

class AccountsIndex extends Component {
    constructor(props) {
        super(props);

        this.state = { accounts: this.props.accounts }
    }

    render() {
        return (
            <div className="animated fadeIn">
                <h4>Current Accounts</h4>
				<hr />
                <Accounts accounts={this.state.accounts} />
            </div>
        );
    }
}

export default AccountsIndex;