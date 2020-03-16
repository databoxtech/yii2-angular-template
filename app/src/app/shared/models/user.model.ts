import { Deserializable } from './deserializable.model';

export class User implements Deserializable{
    id: number;
    displayName: string;
    email: string;
    phone: string;
    permissions: string[];
    jwt: string;
    refresh_token: string;
    role: string;

    deserialize(input: any) {
        Object.assign(this, input);
        return this;
    }
}