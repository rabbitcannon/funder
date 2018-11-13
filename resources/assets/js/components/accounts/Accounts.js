import React, {Component} from "react";
import _ from "underscore";

import AccountItem from './AccountItem';

class Accounts extends Component {
	constructor(props) {
		super(props);

		this.state = {
			accounts: this.props.accounts || []
		}
	}

    render() {
		let accountList = _.map(this.state.accounts, (account, i) => {
			console.log("ID: " + account)
			return <AccountItem key="1" account={account} />
		});

        return (
            <div>
				{accountList}
			</div>
        );
    }
}

export default Accounts;