import React, {Component} from "react";

import AddCreditCard from './AddCreditCard';
import AddCheckingAcct from './AddNewCheckingAcct';
import OneTimeFunding from './OneTimeFunding';
import FundingOptions from "./FundingOptions";

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {balance: this.props.balance ,paymentMethod: null, separator: false}
	}

	componentDidMount = () => {
		$('#add-funds-toggle').on('click', (event) => {
			event.preventDefault();
			this.setState({paymentMethod: "oneTimeFunding", separator: true});
		});
	}

	handleSelection = (event) => {
		this.setState({paymentMethod: event.target.value});
		if(this.state.paymentMethod != "default") {
			this.setState({separator: true});
		}
		else {
			this.setState({separator: false})
		}
	}

	updateBalance = () => {
		this.props.updateBalance();
	}

	renderSeparator = () => {
		if(this.state.separator == true) {
			return(
				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-12">
							<hr />
						</div>
					</div>
				</div>
			);
		}
	}

	addFunds = (page) => {
		this.setState({
			paymentMethod: page
		})
	}

    render() {
		let selection = this.state.paymentMethod;
		let component = null;

		switch(selection) {
			case "new_debit":
				component = <AddCreditCard/>;
				break;
			case "oneTimeFunding":
				component = <OneTimeFunding balance={this.props.balance} updateBalance={this.updateBalance}/>;
				break;
			case "new_checking":
				component = <AddCheckingAcct/>;
				break;
			default:
				component = "Select Above";
		}

        return (
        	<div className="animated fadeIn">
				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-4">
							<FundingOptions paymentMethod={this.state.paymentMethod} separator={this.state.separator}
											handleSelection={this.handleSelection}/>
						</div>

						<div className="cell large-8 text-right">
							<button id="add-funds-toggle" className="button">+ Instant Funds</button>
						</div>
					</div>
				</div>

				{this.renderSeparator()}

				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-12">
							{component}
						</div>
					</div>
				</div>
			</div>


        );
    }
}

export default Index;