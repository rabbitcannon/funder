import React, {Component} from "react";

import AccountItem from './AccountItem';

class Accounts extends Component {
	constructor(props) {
		super(props);

		this.state = {
			accounts: this.props.accounts || []
		}
	}

    render() {
		let accountList = this.state.accounts.map((account, i) => {
			return <AccountItem ref="accounts" key={account.id} account={account} />
		});

        return (
            <div>
				{accountList}
			</div>
        );
    }
}

export default Accounts;