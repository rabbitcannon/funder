import React, {Component} from "react";
import Axios from "axios";
import _ from "underscore";

import Header from "./Header";
import FundingIndex from '../funding/Index';
import AccountIndex from '../accounts/Index';

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {
			cashBalance: 0,
			page: "home",
			playerData: this.props.playerData || {}
		}
	}

	componentDidMount = () => {
		this.updateBalance();
	}

	setPage = (page) => {
		this.setState({ page: page });
	}

	updateBalance = async () => {
		let data = JSON.parse(this.state.playerData);
		let player = data.player;
		let hash = player.playerhash;

		await Axios.get('/api/funds/balance/' + hash).then((response) => {
			this.setState({
				cashBalance: response.data.balance,
			});

			$('#balance-loader').hide();
			$('#balance').show();
		}).catch(function(error) {
			console.log(error);
		});
	}

    render() {
		let data = JSON.parse(this.state.playerData);
		// let accounts = data.accounts;
		let page = this.state.page;
		let currentPage = null;

		switch(page) {
			case "home":
				currentPage = "Welcome!";
				break;
			case "accounts":
				currentPage = <AccountIndex />;
				break;
			case "deposits":
				currentPage = <FundingIndex balance={this.state.cashBalance} updateBalance={this.updateBalance} />;
				break;
			default:
				currentPage = null;
		}

        return (
            <div>
				<Header playerData={this.state.playerData} setPage={this.setPage.bind(this)} balance={this.state.cashBalance} />
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