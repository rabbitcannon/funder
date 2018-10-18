import React, {Component} from "react";
// import Moment from 'momentjs';

class AccountItem extends Component {
	constructor(props) {
		super(props);
	}

    render() {
		let balance = parseFloat(this.props.account.balance).toFixed(2);
		// let name = this.props.account.name.replace(/^\w/, c => c.toUpperCase());
		// let name = this.props.account.name.replace(/^\w/, c => c.toUpperCase());
		let name = this.props.account.name;

        return (
            <div className="animated fadeInUp">
				<h5>{name} <small>Balance: ${balance}</small></h5>
			</div>
        );
    }
}

export default AccountItem;