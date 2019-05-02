import React, { useState } from 'react';
import Content from './Content';
import withGraphQl from './hoc/withGraphQl';
import withRest from './hoc/withRest';
import {
  PageWrapper,
  SwitchWrapper,
  StyledSwitch,
  WordPressPageWrapper
} from './ui';
import 'typeface-roboto';

const App = () => {
  const [graphql, toggleGraphql] = useState(false);
  const ContentWithData = graphql ? withGraphQl(Content) : withRest(Content);
  return (
    <PageWrapper>
      <SwitchWrapper>
        <StyledSwitch checked={graphql} onChange={toggleGraphql} />
        Use GraphQl
      </SwitchWrapper>
      <WordPressPageWrapper>
        <ContentWithData />
      </WordPressPageWrapper>
    </PageWrapper>
  );
};

export default App;
