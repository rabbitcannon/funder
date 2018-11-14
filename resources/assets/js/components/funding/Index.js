import React, {Component} from "react";
import Axios from "axios";
import _ from "underscore";

import AddCreditCard from './AddCreditCard';
import AddCheckingAcct from './AddNewCheckingAcct';
import OneTimeFunding from './OneTimeFunding';
import FundingOptions from "./FundingOptions";
import FundingMethodDetails from "./FundingMethodDetails";

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {
			allProfiles: [],
			balance: this.props.balance,
			existingMethod: null,
			paymentMethod: null,
			paymentType: null,
			separator: false
		}
	}

	componentDidMount = () => {
		$('#add-funds-toggle').on('click', (event) => {
			event.preventDefault();
			this.setState({paymentMethod: "oneTimeFunding", separator: true});
		});
	}

	updatePaymentMethods = async () => {
		$("#loader").show();
		$('#funding-methods option:first').text('Loading...');
		$('#funding-methods').prop('disabled', true);

		let data = JSON.parse(sessionStorage.getItem('playerData'));

		await Axios.post('/api/methods', {
			playerHash: data.player.playerhash,
		}).then((response) =>{
			let data = response.data;
			let profiles = _.omit(data, "addresses");
			this.setState({
				allProfiles: profiles,
			})
			$('#funding-methods option:first').text("--Select One--");
			$('#funding-methods').prop('disabled', false);
			$("#loader").hide();
		}).catch((error) => {
			console.log(error);
		});
	}

	handleSelection = (event) => {
		let selectedID = $('#funding-methods').find('option:selected').attr('id');
		let selectedValue = $('#funding-methods').val();
		let profile = this.state.allProfiles;

		if(selectedValue === "card") {
			let selectedProfile = profile.card_profiles[selectedID];
			this.setState({existingMethod: selectedProfile, paymentType: "card_profile"});
		}

		if(selectedValue === "eft") {
			let selectedProfile = profile.eft_profiles[selectedID];
			this.setState({existingMethod: selectedProfile, paymentType: "eft_profile"});
		}

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
				component = <AddCreditCard updatePaymentMethods={this.updatePaymentMethods}/>;
				break;
			case "oneTimeFunding":
				component = <OneTimeFunding balance={this.props.balance} updateBalance={this.updateBalance}
											updatePaymentMethods={this.updatePaymentMethods} />;
				break;
			case "new_checking":
				component = <AddCheckingAcct/>;
				break;
			case "card":
				component = <FundingMethodDetails balance={this.props.balance} paymentMethods={this.state.paymentMethod}
												  paymentType={this.state.paymentType}
												  updateBalance={this.updateBalance}
												  existingMethod={this.state.existingMethod} />;
				break;
			default:
				component = "Select Above";
		}

        return (
        	<div className="animated fadeIn">
				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-4">
							<FundingOptions refs={this.child} paymentMethod={this.state.paymentMethod} separator={this.state.separator}
											allProfiles={this.state.allProfiles}
											handleSelection={this.handleSelection}
											updatePaymentMethods={this.updatePaymentMethods} />
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