import React, {Component} from "react";

class Header extends Component {
	constructor(props) {
		super(props);

		this.state = {
			playerData: this.props.playerData || {}
		}
	}

	handleLogout = () => {
		localStorage.clear();
		sessionStorage.clear();
		location.reload();
	}

    render() {
		let data = JSON.parse(this.state.playerData);
		let player = data.player;
		let cashBalance = parseFloat(player.cashbalancepence).toFixed(2);

        return (
			<div className="top-bar margin">
				<div className="top-bar-left">
					<ul className="menu">
						<li className="menu-text title">Funder</li>
						<li>
							<a onClick={() => this.props.setPage("deposits")}>Deposit</a>
						</li>
						<li><a onClick={() => this.props.setPage("accounts")}>Accounts</a></li>
					</ul>
				</div>
				<div className="top-bar-right">
					<ul className="menu __logout">
						<li className="menu-text">Balance: ${cashBalance}</li>
						<li className="menu-text">Hi, {player.firstname}!</li>
						<li>
							<a href="#" onClick={this.handleLogout.bind(this)}>Logout</a>
						</li>
					</ul>
				</div>
			</div>
        );
    }
}

export default Header;