import React, {Component} from "react";

class Header extends Component {
	handleLogout = () => {
		localStorage.clear();
		sessionStorage.clear();
		location.reload();
	}

    render() {
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