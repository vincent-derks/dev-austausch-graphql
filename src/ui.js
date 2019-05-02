import styled from 'styled-components';
import Switch from 'react-ios-switch';

export const PageWrapper = styled.div`
  font-family: 'Roboto', sans-serif;
  position: relative;
  width: calc(100vw - 100px);
  left: 50%;
  transform: translateX(-50%);
  min-height: calc(100vh - 50px);
  display: flex;
  flex-direction: column;
`;

export const SwitchWrapper = styled.div`
  display: flex;
  align-items: center;
  justify-content: flex-end;
`;

export const StyledSwitch = styled(Switch)`
  margin-right: 1rem;
`;

export const WordPressPageWrapper = styled.div`
  border: 3px solid grey;
  margin-top: 2rem;
  padding: 1rem;
  flex: 1;
`;
