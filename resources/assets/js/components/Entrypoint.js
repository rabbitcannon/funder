import "babel-polyfill";
import React, { Component } from "react";
import ReactDOM from "react-dom";

import FundingIndex from "./funding/index";
import LoginForm from "./login/LoginForm";
import Axios from "axios";

class EntryPoint extends Component {
    constructor(props) {
        super(props);

        this.state ={loggedIn: false, playerData: []}
    }

    componentDidMount = () => {
    	if(sessionStorage.playerData != null) {
			this.setState({loggedIn: true});
		}
	}

	handleSubmit = (event) => {
		event.preventDefault();

		Axios.post('/api/funding/login', {
			email: $('[name="email"]').val(),
			password: $('[name="password"]').val(),
			registrar_id: $('[name="registrar_id"]').val()
		}).then(function (response) {
			sessionStorage.setItem('playerData', JSON.stringify(response.data));
			this.state.playerData.push(response.data)
			this.setState({
				loggedIn: true,
			}, console.log(this.state.playerData));
		}.bind(this)).catch(function (error) {
			console.log(error);
		});
	}



    renderLoginPanel = () => {
        return (
            <div>
                <LoginForm auth={this.state.loggedIn} handleSubmit={this.handleSubmit.bind(this)}/>
            </div>
        );
    }

    renderProfilePanel = () => {
		return (
			<div>
				<FundingIndex />
			</div>
		);
    }

    render() {
		if(this.state.loggedIn === true) {
		    return this.renderProfilePanel()
		}
		else {
		    return this.renderLoginPanel();
        }
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<EntryPoint />, document.getElementById('app'));
}
