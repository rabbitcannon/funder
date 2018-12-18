import React, {Component} from "react";

import Accounts from "./Accounts";
import Axios from "axios";

class AccountsIndex extends Component {
    constructor(props) {
        super(props);

		this.state = {
			count: null,
			current: 1,
			itemsPerPage: 10,
			transactions: []
		}
    }

	componentDidMount = () => {
    	$('div#acct-paginator').hide();
		this.getAccountHistory();
	}

	getAccountHistory = (page) => {
    	$('#loader').show();
		let data = JSON.parse(sessionStorage.getItem('playerData'));

		Axios.post('/api/account/history', {
			playerHash: data.player.playerhash,
			currentPageQueried: page || this.state.current,
			itemsPerPage: this.state.itemsPerPage
		}).then((response) => {
			let transactions = response.data.Transactions;
			this.setState({
				count: response.data.total_count,
				current: page,
				transactions: transactions
			});
			$('div#acct-paginator').show();
			$('#loader').hide();
		}).catch((error) => {
			console.log(error);
		});
	}

	onShowSizeChange = (current, pageSize) => {
		this.setState({ itemsPerPage: pageSize });
	}

    render() {
        return (
            <div className="animated fadeIn">
				<div className="card animated fadeIn">
					<div className="card-divider">
						<h4>
							Account History
						</h4>
                    </div>

					<div className="card-section">
                        <Accounts count={this.state.count}
								  current={this.state.current}
								  itemsPerPage={this.state.itemsPerPage}
								  transactions={this.state.transactions}
								  onShowSizeChange={this.onShowSizeChange}
								  onChange={this.getAccountHistory} />

						<div className="text-center">
							<span id="loader" style={{display: "hidden"}}>
								<img src="../../images/loader.svg" />
							</span>
						</div>
                    </div>

                </div>
            </div>
        );
    }
}

export default AccountsIndex;