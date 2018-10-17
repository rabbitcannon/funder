import React, {Component} from "react";
import Axios from "axios";

import Address from '../layout/controls/Address';
import CreditCard from "../layout/controls/CreditCard";

import config from  "../../config/config.json";
import FundAmount from "../layout/controls/FundAmount";
import FundingBlock from "./FundingBlock";

const API_KEY = btoa(config.keys.paysafe);
let instance = null;

let OPTIONS = {
	environment: "TEST",
	fields: {
		cardNumber: {
			fieldName: "#card-number",
			placeholder: "card number",
			selector: "#card-number"
		},
		expiryDate: {
			fieldName: "#exp-date",
			placeholder: "MM/YY",
			selector: "#exp-date",
		},
		cvv: {
			fieldName: "#cvv",
			placeholder: "cvv",
			selector: "#cvv",
		}
	}
};

class OneTimeFinding extends Component {
	constructor(props) {
		super(props);

		this.state = {
			additionalAmount: 0,
			newAmount: 0
		}
	}

	componentDidMount = async () => {
		$(document).foundation(); this.handlePayment();

		await window.paysafe.fields.setup(API_KEY, OPTIONS, function(paysafeInstance, error) {
			if(error) {
				console.log("Setup error: " + error.code + " " + error.detailedMessage);
			}
			else {
				instance = paysafeInstance;
			}
		});
	}

	handleAmountChange = (event) => {
		let currency = parseFloat(event.target.value).toFixed(2);
		let newBalance = currency + this.state.newAmount;
		let newBalanceFormatted = parseFloat(newBalance).toFixed(2);

		this.setState({
			additionalAmount: currency, newAmount: newBalanceFormatted
		});
	}

	handlePayment = () => {
		let $errorSpan = $("#form-submit-error");
		$errorSpan.text("");

		$("form#add-card-form").bind("formvalid.zf.abide", function(event, target) {
			event.preventDefault();

			if(!instance) {
				console.log("No instance");
			}

			instance.tokenize(function(paysafeInstance, error, result) {
				console.log(result);
				if(error) {
					$errorSpan.text("Tokenization error: " + error.code + " " + error.detailedMessage)
					console.log("Tokenization error: " + error.code + " " + error.detailedMessage);
				}
				else {
					Axios.post('/api/funds/add', {
						data: {
							amount: $('#fund-amount').val(),
							provider_temporary_token: result.token,
							funding_method_type: "card_profile",
							billing_details: {
								address_nickname: $('#account-nickname').val(),
								address1: $('#address_1').val(),
								address2: $('#address_2').val(),
								city: $('#city').val(),
								state: $('#state').val(),
								country: $('#account-nickname').val(),
								zip: $('#zip').val(),
							}
						}
					}).then(function (response) {
						console.log(response);
					}).bind(this).catch(function (error) {
						console.log(error);
					});
					// window.location.replace("/api/methods/add/" + result.token);
				}
				return false;
			});
		});
	}

    render() {
		const styles = {
			hidden: {
				display: 'none'
			}
		}

        return (
            <div className="card animated fadeIn">
                <div className="card-divider">
                    <h4>Add funds</h4>
                </div>

                <div className="card-section">

					<div>
						<FundingBlock additionalAmount={this.state.additionalAmount} newAmount={this.state.newAmount}/>
					</div>

                    <form id="add-checking-form" data-abide noValidate>
                        <div className="grid-container">

                            <div className="grid-x grid-margin-x">
                                <div className="cell medium-12">
                                    <div data-abide-error className="alert callout" style={styles.hidden}>
                                        <p><i className="fi-alert"></i> There are some errors in your form.</p>
                                    </div>
                                </div>
                            </div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-6">
                                    <Address/>
								</div>
								<div className="cell medium-6">
									<CreditCard/>
									<FundAmount handleAmountChange={this.handleAmountChange}/>
								</div>
							</div>

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12 text-center">
									<button id="pay-now" className="button" onClick={this.handlePayment}>Add Funds</button>
								</div>
							</div>
                        </div>
                    </form>

                </div>
            </div>
        );
    }
}

export default OneTimeFinding;