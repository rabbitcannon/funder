import React, {Component} from "react";

import Header from "./Header";
import DepositsIndex from '../deposits/Index';
import AccountIndex from '../accounts/Index';

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
		let acctData = JSON.parse(this.state.playerData);
		let accounts = acctData.accounts;
		let page = this.state.page;
		let currentPage = null;

		switch(page) {
			case "home":
				currentPage = "Welcome!";
				break;
			case "accounts":
				currentPage = <AccountIndex accounts={accounts}/>;
				break;
			case "deposits":
				currentPage = <DepositsIndex/>;
				break;
			default:
				currentPage = null;
		}

        return (
            <div>
				<Header playerData={this.state.playerData} setPage={this.setPage.bind(this)} />
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