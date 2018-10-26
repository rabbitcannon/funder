import React, {Component} from "react";

import Header from "./Header";
import DepositsIndex from '../deposits/Index';
import AccountIndex from '../accounts/Index';
import _ from "underscore";

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {
			page: "home",
			playerData: this.props.playerData || {}
		}
	}

	setPage = (page) => {
		this.setState({ page: page });
	}

    render() {
		let data = JSON.parse(this.state.playerData);
		let accounts = data.accounts;
		let page = this.state.page;
		let currentPage = null;
		let balance = 0;

		_.each(accounts, function(index) {
			balance += index.balance;
		});

		switch(page) {
			case "home":
				currentPage = "Welcome!";
				break;
			case "accounts":
				currentPage = <AccountIndex accounts={accounts}/>;
				break;
			case "deposits":
				currentPage = <DepositsIndex balance={balance}/>;
				break;
			default:
				currentPage = null;
		}

        return (
            <div>
				<Header playerData={this.state.playerData} setPage={this.setPage.bind(this)} balance={balance} />
				<div id="content" className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell small-12">
							{currentPage}
						</div>
					</div>
				</div>
			</div>
        );
    }
}

export default Index;