import React, {Component} from "react";

import Header from "../layout/Header";
import FundingMethods from './FundingMethods';

class Index extends Component {
	constructor(props) {
		super(props);
	}

    render() {
        return (
            <div>
				<Header />
				<FundingMethods/>
			</div>
        );
    }
}

export default Index;