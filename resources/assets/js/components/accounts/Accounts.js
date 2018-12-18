import React, {Component} from "react";
import Select from "rc-select";
import Pagination from "rc-pagination";
import _ from "underscore";

import localeInfo from 'rc-pagination/lib/locale/en_US';

import AccountItem from './AccountItem';

const itemRender = (current, type, element) => {
	if (type === 'page') {
		return <a href={`#${current}`}>{current}</a>;
	}
	return element;
};

class Accounts extends Component {
	constructor(props) {
		super(props);
	}

    render() {

		let accountList = _.map(this.props.transactions, (transaction, index) => {
			return <AccountItem key={index} transaction={transaction} />
		});

        return (
            <div>
				<table>
					<thead>
					<tr>
						<th className="text-center" width="200">Date</th>
						<th className="text-center">Type</th>
						<th className="text-center" width="250">Authorization Code</th>
						<th className="text-center" width="150">Status</th>
						<th className="text-center" width="150">Amount</th>
					</tr>
					</thead>
					<tbody>
						{accountList}
					</tbody>
				</table>

				<div id="acct-paginator" className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell medium-offset-3 medium-6 text-center">
							<Pagination onChange={this.props.onChange} current={this.props.current} itemRender={itemRender}
										locale={localeInfo} showSizeChanger defaultPageSize={this.props.itemsPerPage}
										total={this.props.count} selectComponentClass={Select} onShowSizeChange={this.props.onShowSizeChange}
										showTotal={(total) => `Total ${total} items`} />
							<small>Total {this.props.count} items</small>
						</div>
					</div>
				</div>
			</div>
        );
    }
}

export default Accounts;