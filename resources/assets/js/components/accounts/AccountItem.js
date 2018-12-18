import React, {Component} from "react";
import Moment from 'moment';

class AccountItem extends Component {
	constructor(props) {
		super(props);
	}

    render() {
		let transaction = this.props.transaction;
		let amount = transaction.amount / 100;
		let timeFormat = Moment.unix(transaction.transaction_datetime).format("MM/DD/YYYY");

		return (
			<tr className="animated fadeIn text-center">
				<td>{timeFormat}</td>
				<td>{transaction.event_type}</td>
				<td>{transaction.authorization_code}</td>
				<td>{transaction.event_state}</td>
				<td>
					{
						(transaction.ledger_type === "credit")
						? <span className="success">+ ${amount.toFixed(2)}</span>
						: <span className="error">- ${amount.toFixed(2)}</span>
					}

				</td>
			</tr>
        );
    }
}

export default AccountItem;