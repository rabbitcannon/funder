import React, {Component} from "react";

class ExistingProfile extends Component {
    constructor(props) {
        super(props);

        this.state = {
            paymentType: null,
            currentMethod: this.props.existingMethod,
        }
    }
    componentDidMount = () => {
        this.determinePaymentType();

		this.props.existingMethod
    }

    determinePaymentType = () => {
        let paymentMethod = this.props.existingMethod;

        if(paymentMethod.includes("card")) {
            this.setState({
                paymentType: "Card Payment"
            })
        }
    }

    render() {
        console.log(this.props.paymentMethods)
        return (
			<div className="card animated fadeIn">
				<div className="card-divider">
					<h4>Existing Payment Method </h4>
				</div>
				<div className="card-section">
                    Current method id: {this.state.existingMethod}
                </div>
            </div>
        );
    }
}

export default ExistingProfile;