import React, {Component} from "react";

class Header extends Component {
	constructor(props) {
		super(props);

		this.state = {playerData: this.props.playerData || {}}
	}

	handleLogout = () => {
		localStorage.clear();
		sessionStorage.clear();
		location.reload();
	}

    render() {
		let data = JSON.parse(this.state.playerData);
		let player = data.player;

        return (
			<div className="top-bar margin">
				<div className="top-bar-left">
					<ul className="menu">
						<li className="menu-text title">Funder</li>
						<li>
							<a href="#">Deposit</a>
						</li>
						<li><a href="#">Accounts</a></li>
					</ul>
				</div>
				<div className="top-bar-right">
					<ul className="menu __logout">
						<li className="menu-text">Balance: ${player.cashbalancepence}</li>
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