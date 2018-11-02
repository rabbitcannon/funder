import React, {Component} from "react";
import _ from "underscore";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faSignOutAlt } from "@fortawesome/free-solid-svg-icons";

class Header extends Component {
	constructor(props) {
		super(props);

		this.state = {
			currentBalance: this.props.balance,
			playerData: this.props.playerData || {},
		}
	}

	componentWillReceiveProps(nextProps) {
		if(this.props != nextProps) {
			this.setState({
				currentBalance: nextProps.balance
			});
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

		const balance = this.state.currentBalance;
		let cashBalance = balance / 100;

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
						<li className="menu-text">Balance:&nbsp;
							<span className="animated fadeIn" id="balance-loader" >
								<img src="../../images/loaders/loader_pink_17_sharp.svg" />
							</span>
							<span id="balance">
								${cashBalance.toFixed(2)}
							</span>
						</li>
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