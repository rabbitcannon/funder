import React, {Component} from "react";
import _ from "underscore";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faSignOutAlt } from "@fortawesome/free-solid-svg-icons";

class Header extends Component {
	constructor(props) {
		super(props);

		this.state = {
			playerData: this.props.playerData || {},
			cashBalance: null,
		}
	}

	componentDidMount =() => {
		let player = JSON.parse(this.state.playerData);
		let accounts = player.accounts;
		let balance = 0;

		_.each(accounts, function(index) {
			balance += index.balance;
		});
	}

	handleLogout = () => {
		localStorage.clear();
		sessionStorage.clear();
		location.reload();
	}

    render() {
		let data = JSON.parse(this.state.playerData);
		let player = data.player;
		let accounts = data.accounts;
		let balance = 0;

		_.each(accounts, function(index) {
			balance += index.balance;
		});

		let cashBalance = balance.toFixed(2);

        return (
			<div className="top-bar margin animated fadeIn">
				<div className="top-bar-left">
					<ul className="menu">
						<li className="menu-text title">Funder</li>
						<li>
							<a onClick={() => this.props.setPage("deposits")}>Deposit</a>
						</li>
						<li>
							<a onClick={() => this.props.setPage("accounts")}>Accounts</a>
						</li>
					</ul>
				</div>
				<div className="top-bar-right">
					<ul className="menu __logout">
						<li className="menu-text">Balance: ${cashBalance}</li>
						<li className="menu-text">Hi, {player.firstname}!</li>
						<li>
							<a href="#" onClick={this.handleLogout.bind(this)}>
								<span><FontAwesomeIcon icon={faSignOutAlt} /></span> Logout
							</a>
						</li>
					</ul>
				</div>
			</div>
        );
    }
}

export default Header;